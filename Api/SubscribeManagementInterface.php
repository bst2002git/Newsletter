<?php
namespace Majidian\Newsletter\Api;

interface SubscribeManagementInterface
{
    /**
     * @param string $email
     * @return string
     */
    public function subscribe(
        string $email
    );
}
