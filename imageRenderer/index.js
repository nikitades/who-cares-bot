import captureWebsite from 'capture-website';
import http from 'http';
import fs from 'fs';

const env = process.env.NODE_ENV || 'local';
const appAddress = process.env.MAIN_APP_ADDRESS || 'http://localhost:8080';

const requestListener = async function (req, res) {
    if (req.url === '/ping') {
        res.setHeader('Content-type', 'text/plain');
        res.writeHead('200');
        res.end('pong');
        return;
    }
    console.log('requesting ' + appAddress + req.url);
    await captureWebsite.file(appAddress + req.url, './file.png', {
        width: 800,
        height: 600,
        launchOptions: {
            args: ['--no-sandbox']
        }
    });
    console.log('request ' + appAddress + req.url + ' processed, sending to client');
    writeImageToClient(res);
}

function writeImageToClient(res) {
    const fileStream = fs.createReadStream('./file.png');
    fileStream.on('open', () => {
        res.setHeader('Content-type', 'image/png');
        fileStream.pipe(res);
        fs.unlinkSync('./file.png');
    });
    fileStream.on('error', (e) => {
        res.setHeader('Content-type', 'text/plain');
        res.writeHead(500);
        res.end(e.message);
    });
}

const rendererAddres = env === 'local' ? { port: 9000, host: '0.0.0.0' } : { port: 80, host: '0.0.0.0' };

const server = http.createServer(requestListener);
server.on('listening', () => {
    console.log('Started on ' + rendererAddres.host + ':' + rendererAddres.port);
});
server.listen(rendererAddres.port, rendererAddres.host);