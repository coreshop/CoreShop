<?php

namespace CoreShop\Component\Resource\TokenGenerator;

class JWTGenerator implements OrderTokenGeneratorInterface
{

    public function generate(int $length)
    {
        return "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c";
    }

    public function validateToken(string $token)
    {
        // TODO: Implement validateToken() method.
    }
}