<?php
require_once '../backend/connection.php';
function sendPushoverNotification($message) {
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => [
                                'token' => PUSHOVER_TOKEN,
                                'user' => PUSHOVER_USER,
                                'message' => $message,
                            ],
                            CURLOPT_RETURNTRANSFER => true,
                        ]);
                        $response = curl_exec($curl);
                        if (!$response) {
                            $error_message = curl_error($curl);
                            logAction("Pushover API call failed", null, "Error: $error_message");
                        }
                        curl_close($curl);
                    }
?>