<?php

namespace App\Services;

class HttpClient
{
    protected array $headers = [];
    protected array $options = [];

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function withToken(string $token): self
    {
        $this->headers['Authorization'] = 'Bearer ' . $token;
        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function acceptJson(): self
    {
        $this->headers['Accept'] = 'application/json';
        return $this;
    }

    public function asForm(): self
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        return $this;
    }

    public function post(string $url, array $data = []): HttpResponse
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/x-www-form-urlencoded') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            $this->headers['Content-Type'] = 'application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $curlHeaders = [];
        foreach ($this->headers as $key => $value) {
            $curlHeaders[] = "{$key}: {$value}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        if (isset($this->options['verify']) && $this->options['verify'] === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (isset($this->options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->options['timeout']);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return new HttpResponse($statusCode, $response ?: $error, !$error);
    }

    public function get(string $url): HttpResponse
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $curlHeaders = [];
        foreach ($this->headers as $key => $value) {
            $curlHeaders[] = "{$key}: {$value}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        if (isset($this->options['verify']) && $this->options['verify'] === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (isset($this->options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->options['timeout']);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return new HttpResponse($statusCode, $response ?: $error, !$error);
    }
}

class HttpResponse
{
    public function __construct(
        protected int $statusCode,
        protected string $body,
        protected bool $success
    ) {}

    public function successful(): bool
    {
        return $this->success && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function json(): ?array
    {
        return json_decode($this->body, true);
    }
}
