run 
```shell
 cp .env.example .env
```

To run docker compose
```shell
 ./vendor/bin/sail up -d
 ./vendor/bin/sail ps
```

To get ip of container to connect with database
```shell
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container-name
```


/etc/hosts\
127.0.0.1  twiva.sail\
127.0.0.1  business.twiva.sail\
127.0.0.1  peter.twiva.sail\
127.0.0.1  api.twiva.sail\
127.0.0.1  influencer.twiva.sail



/home/hossien/Documents/zaman/twiva-new/Modules/Front/Resources/landing/assets/images/Website_Video_Webm.webm
