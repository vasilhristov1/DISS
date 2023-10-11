<?php
    //user Ratchet
    namespace PISSProject;
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    //create our Online Table Class
    class OnlineTable implements MessageComponentInterface {
        //holding clients
        protected $clients;
        //holding cells
        protected $cellsCSS = array();
        //holding clients' ID
        protected $clientsIds = array();
        //holding cell's owner
        protected $cellOwner = array();
        //holding table's rows count
        protected $rowsNumber = 30;
        //holding table's columns count
        protected $columnsNumber = 30;
        //boolean storing if the server is started for the first time
        protected $serverStartedForTheFirstTime;
        //holding cells' classes
        protected $cellsClasses = array();

        //constructor
        public function __construct() {
            $this->clients = new \SplObjectStorage;
            $this->serverStartedForTheFirstTime = true;
            echo "Server started\n";
        }

        // when user connects to the socket
        public function onOpen(ConnectionInterface $conn) {
            // add user to clients array
            $this->clients->attach($conn);

            // create random id for each user
            $randomId = rand(1, 1000000000);

            // map each user with its id
            $this->clientsIds[$conn->resourceId] = $randomId;

            // send information about the current table to the connected user
            $conn->send("jsonTable-" . json_encode($this->cells) . "-" . $this->rowsNumber . '-' . $this->columnsNumber . "-" . json_encode($this->cellsCSS));
            $conn->send("loadIcons-" . json_encode($this->clientsIds));
            $conn->send("loadCellOwners-" . json_encode($this->cellOwner));
            $conn->send("loadCellClasses|" . json_encode($this->cellsClasses));
            
            // message each client that a new user is connected
            foreach ($this->clients as $client) {
                if ($conn !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send("loadNewUserIcon-" . "user" . $randomId);
                }
            }
            
            // load saved table
            if ($this->serverStartedForTheFirstTime) {
                $conn->send("loadSavedTable");
                $this->serverStartedForTheFirstTime = false;
            }

            echo "New connection! ({$conn->resourceId})\n";
        }

        // when server receives message from the clients 
        public function onMessage(ConnectionInterface $from, $msg) {
            $numRecv = count($this->clients) - 1;

            // We use "-" as seperator between different words
            $strings = explode("-", $msg);

            // when cell has its text changed
            if ($strings[0] === "changeCell") {
                $this->cells[$strings[1]] = $strings[2];
            }

            // when a new row is inserted update the rows count
            if ($strings[0] === "insertRow") {
                $this->rowsNumber++;
            }

            // when a new column is inserted update the columns count
            if ($strings[0] === "insertColumn") {
                $this->columnsNumber++;
            }

            // when logged user writes/locks a cell
            if (str_contains($strings[0], "loggedUserChangeCell")) {
                $userOwner = explode("_", $strings[0])[1];
                if ($userOwner !== "undefined") {
                    $this->cellOwner[$strings[1]] = $userOwner;
                }
            }

            // reset the table information
            if ($strings[0] === "loadNewTable") {
                $this->cells = array();
                $this->cellsCSS = array();
                $this->cellOwner = array();
                $this->cellsClasses = array();
                $this->rowsNumber = 30;
                $this->columnsNumber = 30;
            }
            // change cell's class
            if ($strings[0] === "changeClass") {
                $cellData = explode("-", $msg, 3);     
                $this->cellsClasses[$cellData[1]] = $cellData[2];
            }

            // edit cells array
            if ($strings[0] === "editServerCellsArray") {
                $this->cells[$strings[1]] = $strings[2];
            }
            
            // message clients what is the selected cell by the user who is messaging the server
            else if ($strings[0] === "selectedCell") {
                $fullMessage = $msg . "-user-" . $this->clientsIds[$from->resourceId];

                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        // The sender is not the receiver, send to each client connected
                        $client->send($fullMessage);
                    }
                }
            }
            else {
                echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        // The sender is not the receiver, send to each client connected
                        $client->send($msg);
                    }
                }
            }
        }

        // when user disconencts
        public function onClose(ConnectionInterface $conn) {
            // the connection is closed, remove it, as we can no longer send it messages
            $this->clients->detach($conn);

            // remove the icon of the disconnected user
            foreach ($this->clients as $client) {
                $client->send("removeIcon-" . "user" . $this->clientsIds[$conn->resourceId]);
            }

            // remove the selected cell from the disconnecting user
            foreach ($this->clients as $client) {
                $client->send("removeActiveCell-user-" . $this->clientsIds[$conn->resourceId]);
            }
            
            // remove the client id of this user
            unset($this->clientsIds[$conn->resourceId]);

            // send a message in the server console
            echo "Connection {$conn->resourceId} has disconnected\n";
        }

        // if an error occurs close the connection and print the error in the server console
        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "An error has occurred: {$e->getMessage()}\n";

            $conn->close();
        }
    }
?>