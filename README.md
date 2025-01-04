# RtShop
## Poznámky

věci jsem se učil za pochodu (snipety, controls...)

nevěnoval jsem moc pozornost vzhledu - pochopil jsem, že vám spíše jde o backend

na tomto projektu jsem strávil čistého času 8H, dělal jsem to ovšem s přestávkama

## Usage

### Možnost 1 - git hosting (codespace)
vytvoříte nový codespace v tomto repozitáři

napíšete sh buildphp.sh

pak docker-compose up pro běh programu, měl by se otevřít port 80 pro aplikaci a 8090 pro phpadmin

### Možnost 2 - wsl (linux)
v linuxu vytvoříte v kořenovém adresáři složku workspace

do této složky clonnete repozitář tak aby readme byl /workspace/RtShop

doporučuji sudo chmod -R a+rw /workspace/RtShop/ aby jste se nedělali s opravněním

jděte do /workspace/RtShop

napište sh buildphp.sh

pak docker-compose up pro běh programu, měl by se otevřít port 80 pro aplikaci a 8090 pro phpadmin

## Time
15 min

13:06 - 40 
13:46

13:48 - 10
13:58

15:18 - 110
17:08

17:08 - 10
17:18

19:20 - 40
20:00

23:00 - 100
0:40

13:00 - 20
13:20

15:04 - 20
15:34

16:35 - 35
17:10

0:35 - 45
1:20

0:00 - 40 commit 8H -full search, tags, basket, orders, paging
0:40 

485
480

## 1.
sudo sh buildphp.sh

to start -> docker-compose up

sudo chmod -R a+rw db_data/
