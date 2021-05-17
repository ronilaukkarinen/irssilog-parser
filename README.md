## Command

`php cli.php logfile.log > logfile.json`

# Loop in log dir

``` shell
for X in *; do
    php cli.php $X > $X.json
done
```
