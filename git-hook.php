<?php #!/usr/bin/env /usr/bin/php
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

exec('cd /home/scfr/JULIET && /usr/lib/git-core/git pull',$output,$return);
exec('cd /home/scfr/JULIET && /usr/local/bin/ng build --prod',$build,$return);

echo "Pull : ";
print_r($output);
echo "Build : ";
print_r($build);

?>
