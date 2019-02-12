# Serverless w Kubernetes + PHP = Kubeless

## Instalacja Kubeless

[<https://kubeless.io/docs/quick-start/]https://kubeless.io/docs/quick-start/>

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

```
$ export OS=$(uname -s| tr '[:upper:]' '[:lower:]')
$ curl -OL https://github.com/kubeless/kubeless/releases/download/$RELEASE/kubeless_$OS-amd64.zip && \
  unzip kubeless_$OS-amd64.zip && \
  sudo mv bundles/kubeless_$OS-amd64/kubeless /usr/local/bin/
```

## Deploy

```bash
$ kubeless function deploy hello --runtime php7.2 --handler hello.foo --from-file hello.php --dependencies composer.json
```

## Wywołanie

```bash
$ kubeless function call hello --data 'hello world!'
```

## Dostęp przez proxy

```bash
$ kubectl proxy -p 8080 &
$ curl -L --data 'hello world!' localhost:8080/api/v1/namespaces/default/services/hello:http-function-port/proxy/ 
```

## Aktualizacja

```bash
$ kubeless function update hello --from-file hello.php
```

## Czyszczenie

```bash
$ kubeless function delete hello
```

## Kafka

```bash
$ export RELEASE=$(curl -s https://api.github.com/repos/kubeless/kafka-trigger/releases/latest | grep tag_name | cut -d '"' -f 4)
$ kubectl create -f https://github.com/kubeless/kafka-trigger/releases/download/$RELEASE/kafka-zookeeper-$RELEASE.yaml
```

```bash
$ kubeless trigger kafka create hello --function-selector created-by=kubeless,function=hello --trigger-topic hello-topic
```

```bash
$ kubeless topic publish --topic hello-topic --data "hello world"
```
