<?php
/**
 * DigiMart Pay - PHP Test Script
 */

echo "<h1>🚀 DigiMart Pay - System Check</h1>";
echo "<p>PHP is working correctly on this server.</p>";
echo "<ul>";
echo "<li><b>PHP Version:</b> " . PHP_VERSION . "</li>";
echo "<li><b>Current Time:</b> " . date('Y-m-d H:i:s') . "</li>";
echo "<li><b>Server Software:</b> " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li><b>Document Root:</b> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li><b>Current Script:</b> " . __FILE__ . "</li>";
echo "</ul>";

echo "<p>✅ If you see this, your routing and PHP environment are functional.</p>";
