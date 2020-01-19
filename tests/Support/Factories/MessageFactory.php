<?php

namespace Tests\Support\Factories;

class MessageFactory
{
    function makePartAsArray($id = 1)
    {
        return [
            "* $id FETCH (BODY[] {2769}\r\n",
            "Subject: test\r\n",
            "To: joe@example.com\r\n",
            "Content-Type: multipart/alternative; boundary=\"0000000000009e67480569e8db24\"\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24\r\n",
            "Content-Type: text/plain; charset=\"UTF-8\"\r\n",
            "\r\n",
            "lorem ipsum\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24\r\n",
            "Content-Type: text/html; charset=\"UTF-8\"\r\n",
            "\r\n",
            "<div dir=\"ltr\">lorem ipsum</div>\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24--\r\n",
            ")\r\n",
        ];
    }
}
