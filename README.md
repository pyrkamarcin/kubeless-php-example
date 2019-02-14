# Serverless w Kubernetes + PHP = Kubeless

## Instalacja Kubeless



Korzystanie z Kubeless wymaga jego instalacji, możesz ją przeprowadzić w następujący sposób:

```bash
$ export RELEASE=$(curl -s https://api.github.com/repos/kubeless/kubeless/releases/latest | grep tag_name | cut -d '"' -f 4)
$ kubectl create ns kubeless
$ kubectl create -f https://github.com/kubeless/kubeless/releases/download/$RELEASE/kubeless-$RELEASE.yaml

$ kubectl get pods -n kubeless
NAME                                           READY     STATUS    RESTARTS   AGE
kubeless-controller-manager-567dcb6c48-ssx8x   1/1       Running   0          1h

$ kubectl get deployment -n kubeless
NAME                          DESIRED   CURRENT   UP-TO-DATE   AVAILABLE   AGE
kubeless-controller-manager   1         1         1            1           1h

$ kubectl get customresourcedefinition
NAME                          AGE
cronjobtriggers.kubeless.io   1h
functions.kubeless.io         1h
httptriggers.kubeless.io      1h
```

Aby uzyskać komendę kubeless, na Linux lub MacOSX należy użyć następującego kodu:

```
$ export OS=$(uname -s| tr '[:upper:]' '[:lower:]')
$ curl -OL https://github.com/kubeless/kubeless/releases/download/$RELEASE/kubeless_$OS-amd64.zip && \
  unzip kubeless_$OS-amd64.zip && \
  sudo mv bundles/kubeless_$OS-amd64/kubeless /usr/local/bin/
```
W przypadku Windowsa należy pobrać najnowszą wersję ze strony https://github.com/kubeless/kubeless/releases a następnie dodać plik binarny Kubeless do zmiennej Path. 

## Deploy

Funkcje w Kubeless tworzy się w poniższy sposób:

```bash
$ kubeless function deploy hello --runtime php7.2 --handler hello.foo --from-file hello.php --dependencies composer.json
```

## Wywołanie

Aby przetestować działanie funkcji możesz użyć następującej komedny:

```bash
$ kubeless function call hello --data 'hello world!'
```

## Dostęp przez proxy

Funkcję przetestować możesz również poprzez użycie dostępu proxy:

```bash
$ kubectl proxy -p 8080 &
$ curl -L --data 'hello world!' localhost:8080/api/v1/namespaces/default/services/hello:http-function-port/proxy/ 
```

## Aktualizacja

Do zaaktualizowania funkcji użyj komendy:

```bash
$ kubeless function update hello --from-file hello.php
```

## Czyszczenie

Usunąć funkcję możesz w poniższy sposób:

```bash
$ kubeless function delete hello
```

## Kafka

Jeśli masz już działający klaster Kafki w tym samym środowisku, możesz w prosty sposób wdrożyć funkcję PubSub.

Aby wdrożyć manifest potrzebny do wdrożenia Kafki i Zookepera, wykonaj następujące polecenia:

```bash
$ export RELEASE=$(curl -s https://api.github.com/repos/kubeless/kafka-trigger/releases/latest | grep tag_name | cut -d '"' -f 4)
$ kubectl create -f https://github.com/kubeless/kafka-trigger/releases/download/$RELEASE/kafka-zookeeper-$RELEASE.yaml
```
Następnie musisz utworzyć wyzwalacz Kafki, który umożliwi skojarzenie funkcji z tematem określonym przez --trigger-topic, w następujący sposób:

```bash
$ kubeless trigger kafka create hello --function-selector created-by=kubeless,function=hello --trigger-topic hello-topic
```
Teraz możesz wywołać funkcję poniższą komendą:

```bash
$ kubeless topic publish --topic hello-topic --data "hello world"
```
## Przydatne linki:
[https://kubeless.io/docs/quick-start/](https://kubeless.io/docs/quick-start/)

[https://kubeless.io/docs/pubsub-functions/](https://kubeless.io/docs/pubsub-functions/)
