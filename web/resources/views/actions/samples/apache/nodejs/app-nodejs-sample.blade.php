var http = require('http');
var fs = require('fs');

http.createServer(function (req, res) {

    res.writeHead(200, {'Content-Type': 'text/html'});

    var readStream = fs.createReadStream(__dirname + '/templates/index.html','utf8');
    readStream.pipe(res);

}).listen(1337, '127.0.0.1');

console.log('Server running at http://127.0.0.1:1337/');
