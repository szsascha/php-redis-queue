# php-redis-queue

A few years ago I created a private non-public php project where I've used redis as a queue to communicate between microservices. This was a pretty easy way to do in the project because redis was also needed for other things. So I could use the existing infrastructure to enable a scalable communication between services.

I stumbled again over this project and decided to make the redis part public. Its not state of the art since its a couple of years old but maybe someone can get some inspiration out of it.

I also extract only the necessary parts for this case from the project. So I created a bit of new code and changed some things to make it more easy to understand.

## Setup

1. Clone the repository
2. Run `composer install`
3. Run `docker-compose up`

## Usage

By default 3 instances of the php application (workers) are started. 1 instance of redis starts for the communication. Redis is available by the default port on localhost.

After the environment has been started, new messages can be sent into the "input" list of the redis server. The next free worker takes this element and put it into the "processing" queue so no other service can get the message the worker is processing. After processing, the worker put its response into the "output" list.