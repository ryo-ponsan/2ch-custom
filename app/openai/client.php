<?php
class OpenAIClient
{
    private static $api_key = '';

    public static function transformComment($comment)
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => '以下の文章を優しい言葉や絵文字を交えた表現に変換してください。返答は不要なので、変換結果のみ出力して。日本語は日本語に、英語は英語に変換して'],
                ['role' => 'user', 'content' => $comment]
            ],
            'temperature' => 0.7,
            'max_tokens' => 100,
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::$api_key,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            log_debug("cURL error: " . curl_error($ch));
            curl_close($ch);
            return $comment;
        }
    
        if ($http_code !== 200) {
            log_debug("HTTP error: $http_code, Response: $response");
            curl_close($ch);
            return $comment;
        }
    
        curl_close($ch);
    
        $response_data = json_decode($response, true);
        log_debug("API Response: " . print_r($response_data, true));
    
        if (!isset($response_data['choices'][0]['message']['content'])) {
            log_debug("Unexpected API response format.");
            return $comment;
        }
    
        return trim($response_data['choices'][0]['message']['content']);
    }
    
}
?>
