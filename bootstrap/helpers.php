<?php

function setErrorResponse(string $message, int $status = 500) {
  return response()->json([
    'message' => $message
  ], $status);
}

function setSuccessResponse(string $message, array $data = [], int $status = 200) {
  return response()->json([
    'message' => $message,
    'data' => $data
  ], $status);
}