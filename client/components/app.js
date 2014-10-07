/**
 * @jsx React.DOM
 */

var React = require('react');
var Router = require('react-router');
var Link = Router.Link;

var App = React.createClass({

	render: function() {
		return (
			<div>

				<header>
					<div className="container">

					     <h1><Link to="index">Losofacebook</Link></h1>

						<ul>
							<li>
								<Link to="wall" params={{ username: 'gaylord.lohiposki' }}><i className="fa fa-home"></i> Home</Link>
							</li>
					    </ul>

					</div>

			    </header>

			    <div className="container">
			    	<this.props.activeRouteHandler/>
			    </div>
			</div>
		);
	}

});

module.exports = App;
