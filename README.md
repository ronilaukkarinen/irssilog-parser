## Command

`php cli.php logfile.log > logfile.json`

# Loop in log dir

``` shell
for X in *; do
  php cli.php $X > $X.json
done
```

# Check for Unicode Characters

``` shell
#!/bin/bash
for X in *; do
   grep -axv '.*' $X
done
```
