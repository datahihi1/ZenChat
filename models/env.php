<?php
const BASE_HOST = 'localhost';
const BASE_PORT = '80';
const DATABASE = 'chat_app';
const DB_USER = 'root';
const DB_PASS = '';

$doc_root = $_SERVER['DOCUMENT_ROOT'];
define('PATH_BASE', $doc_root . "/ZenChat/");

define('UPLOAD_PUBLIC',PATH_BASE . "uploads\public\ " );
