<?php 
namespace App\Services;

class NotificationService
{
    public static function send($message) {
        $webhookUrl = "https://discord.com/api/webhooks/1235529664265457664/Z9n1DFnwkw7VClc8oY9dY4peiA9_0QNLXlMWkrIDW7Goaf1VIRUirwWy0NqC1yX4MVZh";
        // Create the message payload
        $payload = json_encode([
            "content" => $message
        ]);
    
        // Initialize cURL session
        $ch = curl_init($webhookUrl);
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
        // Execute cURL session
        $response = curl_exec($ch);
    
        // Check if any error occurred
        if(curl_error($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
    
        // Close cURL session
        curl_close($ch);
    
        return $response;
    }
}