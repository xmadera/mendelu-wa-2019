<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;


// --- NOT SECURED ROUTES ---

$app->post('/api/register',
    function (Request $request,
              Response $response,
              array $args) {
    $data = $request->getParsedBody();
    if(!empty($data['login']) &&
        !empty($data['password']) &&
       !empty($data['name']) &&
        !empty($data['surname']) &&
       !empty($data['gender']) &&
        !empty($data['email'])) {
        $model = new Users($this->db);
        try {
            $model->register($data);
            return $response->withStatus(201);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    } else {
        return $response->
        withStatus(400);
    }
});

$app->post('/api/login', function(Request $request,
                                  Response $response,
                                  array $args) {
    $data = $request->getParsedBody();
    if (!empty($data['login']) && !empty($data['password'])) {

        try {
            $model = new Users($this->db);
            $user = $model->verify($data['login'], $data['password']);
            if ($user) {
                $signer = new Sha256();

                $token = (new Builder())
                    ->setIssuer('https://akela.mendelu.cz') // Configures the issuer (iss claim)
                    ->setAudience('https://akela.mendelu.cz') // Configures the audience (aud claim)
                    ->setId('mojeSuperIdTetoAplikace', true) // Configures the id (jti claim), replicating as a header item
                    ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                    ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
                    ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
                    ->set('id', $user['id_users']) // Configures a new claim, called "uid"
                    ->set('login', $user['login']) // Configures a new claim, called "uid"
                    ->sign($signer, getenv('TOKEN_KEY')) // creates a signature using our key from .env
                    ->getToken(); // Retrieves the generated token

                return $response->withJson([
                    'token' => (string) $token
                ], 201);
            } else {
                return $response->withStatus(404);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    } else {
        return $response->withStatus(400);
    }
});


// --- FOLLOWING ROUTES ARE SECURED - REQUIRE LOGIN ---

$app->group('/api/auth', function() use ($app) {

    $app->get('/rooms', function (Request $request, Response $response, array $args) {
        $roomsModel = new Rooms($this->db);
        try {
            $data = $roomsModel->all();
            return $response->withJson($data);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->get('/rooms/{id}', function (Request $request, Response $response, array $args) {
        if(!empty($args['id'])) {
            $rm = new Rooms($this->db);
            try {
                $info = $rm->find($args['id']);
                if($info) {
                    return $response->withJson($info);
                } else {
                    return $response->withStatus(404);
                }
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });


    $app->delete('/deleteRoom/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
        $rm = new Rooms($this->db);
        try {
                $rm->delete($args['roomId']);
                return $response->withStatus(201);

        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
        } else {
            return $response->withStatus(400);
        }
    });

    $app->post('/rooms', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['title'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->add($data['title'], $userId);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/saveUserInRoom', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->saveUserInRoom($data['roomId'], $userId);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/kickUser', function (Request $request, Response $response, array $args) {
        $rm = new Users($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) AND !empty($data['userId'])) {
                $rm->kickUser($data['userId'], $data['roomId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/removeKick', function (Request $request, Response $response, array $args) {
        $rm = new Users($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) AND !empty($data['userId'])) {
                $rm->removeKick($data['userId'], $data['roomId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/deleteUserFromRoom', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['userId']) AND !empty($data['roomId'])) {
                $rm->deleteUserFromRoom($data['roomId'], $data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->put('/lockRoom', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId'])) {
                $rm->lockRoom($data['roomId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->put('/updateUser', function (Request $request, Response $response, array $args) {
        $rm = new Users($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['userLogin']) AND !empty($data['gender']) AND !empty($data['name']) AND !empty($data['surname']) AND !empty($data['email'])) {
                $rm->updateUser($data['userLogin'], $data['name'], $data['surname'], $data['email'], $data['gender']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->put('/unlockRoom', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId'])) {
                $rm->unlockRoom($data['roomId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->put('/updateOwner', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) AND !empty($data['userId'])) {
                $rm->updateOwner($data['userId'], $data['roomId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/saveMessage', function (Request $request, Response $response, array $args) {
        $rm = new Messages($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['text']) AND !empty($data['roomId'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->saveMessage($data['roomId'], $userId, $data['text'], $data['recipient']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->get('/messages/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Messages($this->db);
            try {
                $messages = $rm->getAllByRoomId($args['roomId']);
                return $response->withJson($messages);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });


    $app->get('/users/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Users($this->db);
            try {
                $usersInRoom = $rm->inRoom($args['roomId']);
                return $response->withJson($usersInRoom);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });


    $app->get('/user', function (Request $request, Response $response, array $args) {
        $rm = new Users($this->db);
        try {
            $token = $request->getAttribute('token');
            $userLogin = $token->getClaim('login');
            $userData = $rm->getByLogin($userLogin);
            return $response->withJson($userData);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }

    });

    $app->get('/users', function (Request $request, Response $response, array $args) {
        $rm = new Users($this->db);
        try {
            $usersData = $rm->allUsers();
            return $response->withJson($usersData);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }

    });

    $app->get('/kicks', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $kickData = $rm->kicks();
            return $response->withJson($kickData);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }

    });

    // Place for other secured routes like GET /messages.

})->add(function(Request $request, Response $response, $next) {
    $rawToken = $request->getHeaderLine('Authorization');
    if($rawToken) {
        $token = (new Parser())->parse((string) $rawToken);

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer('https://akela.mendelu.cz');
        $data->setAudience('https://akela.mendelu.cz');
        $data->setId('mojeSuperIdTetoAplikace');

        $signer = new Sha256();

        if($token->validate($data) &&
            $token->verify($signer, getenv('TOKEN_KEY'))) {
            $request = $request->withAttribute('token', $token);
            return $next($request, $response, $token);
        }
    }
    return $response->withStatus(401);  //unauthorized
});
