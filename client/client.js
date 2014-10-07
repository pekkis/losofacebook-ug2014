/**
 * @jsx React.DOM
 */

var React = require('react');
var Router = require('react-router');
var Route = Router.Route;
var Routes = Router.Routes;
var DefaultRoute = Router.DefaultRoute;
var Link = Router.Link;

var Losofacebook = require('./components/losofacebook.js');
var App = Losofacebook.App;
var Wall = Losofacebook.Wall;
var Index = Losofacebook.Index;

console.log(Wall);

var currentUser = { username: 'gaylord.lohiposki' };

/**
 * @jsx React.DOM
 */

var React = require('react');

var Router = React.createClass({

	render: function() {
		return (
		  <Routes location="history">
		    <Route name="app" path="/" handler={App}>
		    	<Route name="index" path="/" handler={Index}></Route>
		    	<Route name="wall" path="/person/:username" handler={Wall}></Route>
		    	<DefaultRoute handler={Index}/>
		    </Route>
		  </Routes>
		);
	}
});

React.renderComponent(Router(currentUser), document.getElementById('main'));