const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const bodyParser = require("body-parser");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(bodyParser.json());

const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*",
    },
});

io.on("connection", (socket) => {
    console.log("New client connected:", socket.id);

    socket.on("disconnect", () => {
        console.log("Client disconnected:", socket.id);
    });
});

app.post("/broadcast", (req, res) => {
    const { event, payload, clientId } = req.body;
    console.log("Received broadcast event:", event, "with payload:", payload);

    const senderSocket = clientId ? io.sockets.sockets.get(clientId) : null;

    if (senderSocket) {
        senderSocket.broadcast.emit(event, payload);
    } else {
        io.emit(event, payload);
    }

    res.status(200).json({ message: "Event broadcasted successfully" });
});

const PORT = process.env.PORT || 6001;
server.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
});
