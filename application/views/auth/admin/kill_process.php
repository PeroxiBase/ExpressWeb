<?php

$ProcessId=$_POST['ProcessId'];

exec('kill -9 $ProcessId');
