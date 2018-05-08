// Import
const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');

// Init app
const app = express();

/*
// Middleware
const logger = (req, res, next) => {
	console.log('Request '+ req);
	console.log('Response '+ req);
	next();
}

// Use middleware
app.use(logger);
*/

// Body Parser Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
	extended: false
}));

// Set static path (for vue)
//app.use(express.static(path.join(__dirname, 'public')));

// Routing
app.get('/', (req, res) => {
	res.send('Hello World');
	//res.json(person);
});

// Listen port
app.listen(3000, () => {
	console.log('Server statred at 3000'); 
});
