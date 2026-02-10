<?php
echo "=== private_html SCAN ===\n";
$target = '/home/erguvanpsi/domains/erguvanpsikoloji.com/private_html';
if (is_dir($target)) {
    print_r(scandir($target));
} else {
    echo "private_html NOT found or NOT a directory.\n";
}
