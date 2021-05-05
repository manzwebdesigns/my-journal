@setup
$ssh = getenv('LP_SSH_CMD');
@endsetup

@servers(['web' => $ssh, 'localhost' => '127.0.0.1'])

@story('pull')
backupdb
getdb
cleanup
@endstory

@task('backupdb', ['on' => 'web'])
echo 'Backing up the database on server'
mysqldump -u bud -p"(g^tV4CMPan%BdGtOw" mj | gzip > getenv('LP_DB_BACK_PATH')
@endtask

@task('getdb', ['on' => 'localhost'])
echo 'Getting the database backup from server'
scp getenv('LP_SSH_CMD'):getenv('LP_DB_BACK_PATH') .
@endtask

@task('cleanup', ['on' => 'web'])
echo 'Deleting mj.sql.gz on server'
rm getenv('LP_DB_BACK_PATH')
@endtask
