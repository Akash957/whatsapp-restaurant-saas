<?php

namespace App\Services\Ai;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Http;

class AiConversationService implements AiServiceInterface
{
    public function chat(string $prompt, array $history = [], ?int $restaurantId = null): string
    {
        $apiKey = config('services.ai.api_key'); $model = config('services.ai.model','gpt-4o-mini');
        if (!$apiKey) return $this->fallback($prompt, $restaurantId);
        $messages = array_merge([['role'=>'system','content'=>$this->systemPrompt($restaurantId)]], $history, [['role'=>'user','content'=>$prompt]]);
        $url = config('services.ai.base_url') ?? 'https://api.openai.com/v1/chat/completions';
        $resp = Http::withToken($apiKey)->post($url, ['model'=>$model,'messages'=>$messages,'max_tokens'=>500]);
        return $resp->json('choices.0.message.content') ?? $this->fallback($prompt, $restaurantId);
    }

    public function recommendMenu(string $query, int $restaurantId): string
    {
        $products = \App\Models\Product::where('restaurant_id',$restaurantId)->where('is_available',true)->limit(5)->pluck('name')->join(', ');
        return "Based on '{$query}', I recommend: {$products}. Would you like to order? Reply with the item name!";
    }

    public function handleWhatsApp(WhatsAppConversation $conv, string $text): string
    {
        if($conv->mode === 'human') return '';
        $history = AiConversation::firstOrCreate(['restaurant_id'=>$conv->restaurant_id,'whatsapp_conversation_id'=>$conv->id],['status'=>'open']);
        $msgs = $history->messages()->latest()->limit(10)->get()->reverse()->map(fn($m)=>['role'=>$m->role,'content'=>$m->content])->toArray();
        $reply = $this->chat($text, $msgs, $conv->restaurant_id);
        AiMessage::create(['restaurant_id'=>$conv->restaurant_id,'ai_conversation_id'=>$history->id,'role'=>'user','content'=>$text]);
        AiMessage::create(['restaurant_id'=>$conv->restaurant_id,'ai_conversation_id'=>$history->id,'role'=>'assistant','content'=>$reply]);
        return $reply;
    }

    private function systemPrompt(?int $restaurantId): string
    {
        $restaurant = $restaurantId ? \App\Models\Restaurant::find($restaurantId) : null;
        $name = $restaurant->name ?? 'Restaurant';
        $menu = $restaurant ? \App\Models\Product::where('restaurant_id',$restaurant->id)->where('is_available',true)->limit(20)->pluck('name')->join(', ') : '';
        return "You are a helpful assistant for {$name}. Menu: {$menu}. Answer FAQ about menu, hours, delivery, order tracking. Be concise, friendly. If customer wants to order, guide them. If needing human, say 'Connecting you to staff'.";
    }

    private function fallback(string $prompt, ?int $restaurantId): string
    {
        $q = strtolower($prompt);
        if(str_contains($q,'menu')||str_contains($q,'food')) return $this->recommendMenu($prompt, $restaurantId ?? 1);
        if(str_contains($q,'hour')||str_contains($q,'open')) return "We are open 10 AM - 10 PM daily. Check our menu for more!";
        if(str_contains($q,'track')||str_contains($q,'order')) return "Share your order number to track. Or visit the tracking link.";
        return "Thanks for your message! How can I help? Ask about menu, hours, or order status. Type 'human' for staff.";
    }
}