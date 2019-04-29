:loop
@echo RUNNING DELETE
@c:\xampp\php\php index.php delete
@timeout 5
@REM set /a loopcount=loopcount-1
@REM if %loopcount%==0 goto exitloop
@goto loop