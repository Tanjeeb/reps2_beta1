'use strict';

const moment = require('moment');
const path = require('path');
const fs = require('fs');
const helper = require('./helper');

class Socket {

    constructor(socket) {
        this.io = socket;
    }

    socketEvents() {
        this.io.on('connection', (socket) => {           
            /**
            * get the get messages
            */
            socket.on('getMessages', async () => {                  
                const result = await helper.getMessages();
                if (result === null) {
                    this.io.emit('getMessagesResponse', { result: [] });
                } else {
                    this.io.emit('getMessagesResponse', { result: result });
                }
            });

            /**
            * send the messages to the user
            */
            socket.on('sendMessage', async (response) => {   
                console.log(response.user)           
                this.insertMessage(response.messagePacket);               
                response.messagePacket['created_at'] = moment().utc().format();
                this.io.emit('addMessageResponse', response.messagePacket);
            });

            socket.on('typing', function (data) {
                socket.to(data.socket_id).emit('typing', { typing: data.typing, to_socket_id: socket.id });
            });

            socket.on('upload-image', async (response) => {
                let dir = moment().format("D-M-Y") + "/" + moment().format('x') + "/" + response.fromUserId
                await helper.mkdirSyncRecursive(dir);
                let filepath = dir + "/" + response.fileName;
                var writer = fs.createWriteStream(path.basename('uploads') + "/" + filepath, { encoding: 'base64' });
                writer.write(response.message);
                writer.end();
                writer.on('finish', function () {
                    response.message = response.fileName;
                    response.filePath = filepath;
                    response.date = new moment().format("Y-MM-D");
                    response.time = new moment().format("hh:mm A");
                    this.insertMessage(response, socket);
                    socket.to(response.toSocketId).emit('addMessageResponse', response);
                    socket.emit('image-uploaded', response);
                }.bind(this));
            });

            socket.on('disconnect', async () => {
               
            });
        });
    }

    async insertMessage(data) {
        const sqlResult = await helper.insertMessages({
            user_id: data.user_id,
            user_name: data.user_name,
            file_path: data.file_path,
            imo: data.imo,
            message: data.message,
        });
    }

    socketConfig() {
        this.io.use(async (socket, next) => {
            next();
            // let userId = socket.request._query['id'];
            // let userSocketId = socket.id;
            // const response = await helper.addSocketId( userId, userSocketId);
            // if(response &&  response !== null){
            //     next();
            // }else{
            //     console.error(`Socket connection failed, for  user Id ${userId}.`);
            // }
        });
        this.socketEvents();
    }
}
module.exports = Socket;
