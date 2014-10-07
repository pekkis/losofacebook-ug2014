/**
 * @jsx React.DOM
 */

var React = require('react');

var Index = React.createClass({

	render: function() {
		return (
			<div>
				<h2>[ Welcome to Losofacebook ]</h2>

				<p>
					Losofacebook is an online directory that connects people through social networks at snake oil businesses.
				</p>

				<p>
					We have opened up Losofacebook for popular consumption at <strong>Valkee Korvavalo</strong>.
				</p>

				<p>
					You can use Losofacebook to:
				</p>

				<ul>
					<li>Search for people at your foundation</li>
					<li>Find out who to scam with your snake oil products</li>
					<li>Look up your enemies enemies</li>
					<li>See a visualization of your social network</li>
				</ul>

			</div>
		);
	}

});

module.exports = Index;