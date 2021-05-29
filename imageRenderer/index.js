import captureWebsite from 'capture-website';
import http from 'http';
import fs, { write } from 'fs';
import url from 'url';

const requestListener = async function (req, res) {
    if (req.url.startsWith('/render/top')) {
        const queryObject = url.parse(req.url, true).query;
        console.log(queryObject);
        await captureWebsite.file('http://localhost:8080/markup/top/' + queryObject.renderRequest, './file.png', {
            width: 800,
            height: 600
        });
        writeImageToClient(res);
    }

    if (req.url.startsWith('/render/activity')) {
        const queryObject = url.parse(req.url, true).query;
        console.log(queryObject);
        await captureWebsite.file('http://localhost:8080/markup/activity/' + queryObject.renderRequest, './file.png', {
            width: 800,
            height: 600
        });
        writeImageToClient(res);
    }
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

const server = http.createServer(requestListener);
server.on('listening', () => {
    console.log('Started on 0.0.0.0:9000');
});
server.listen(9000, '0.0.0.0');