<p align="center"><img width="100" src="https://github.com/gloggi/habatu-manager/blob/master/habatu-frontend/src/assets/rotating_ball_a.png"></p>

# HaBaTu Tournament Manager

WebApp für das jährliche Handballturnier des Pfadikorps Glockenhof. Der HaBaTu Tournament Manager ermöglicht es, automatisch einen Spielplan und Finalspiele zu generieren.

## Lokale Installation

Um den HaBaTu Tournament Manager lokal laufen zu lassen benötigst du [Docker](https://docker.io/) und docker-compose. Danach musst du folgende Schritte ausführen:
1. Klone diese Repository:
```
git clone https://github.com/gloggi/habatu-tournament-manager.git
cd habatu-tournament-manager
```
2. Passe im Ordner ```habatu-frontend``` das ```.env.production``` file an

3. Erstelle die Images für die Container:
```
docker-compose -f docker-compose.local.yml build
```
4. Starte die Docker Container:
```
docker-compose -f docker-compose.local.yml up -d
```
5. Rufe im Browser folgende URL auf:
```
http://localhost
```


## Entwicklung
1. Klone diese Repository:
```
git clone https://github.com/gloggi/habatu-tournament-manager.git
cd habatu-tournament-manager
```
2. Passe im Ordner ```habatu-frontend``` das ```.env``` file an.

3. Erstelle die Images für die Container:
```
docker-compose build
```
4. Starte die Docker Container:
```
docker-compose up -d
```
5. Rufe im Browser folgende URL auf:
```
http://localhost:8080
```

## Datenbank Backup

Erstellen eines Datenbank Backup:
```
docker-compose exec -T mongo sh -c 'mongodump --archive' > db.dump
```
Laden eines Datenbank Backup:
```
docker-compose exec -T mongo sh -c 'mongorestore --archive' < db.dump
```