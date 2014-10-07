/**
 * @jsx React.DOM
 */

var React = require('react');

var Friends = require('./friends.js');
var Lussings = require('./lussings.js');

var WallMain = React.createClass({

    backgroundImage: function() {
    	return 'https://place.manatee.lc/' + this.props.person.backgroundId + '/1140/250.jpg';
    },

    urlirizer: function(version) {
    	return '/images/' + this.props.person.primaryImageId + '-' + version + '.jpg';
    },

	render: function() {

		return (
			<div className="row">

			    <div className="col-md-8">
			        <Lussings person={this.props.person}></Lussings>
			    </div>

			    <div className="col-md-4">
			    	<Friends friends={this.props.person.friends}></Friends>
			    </div>
			</div>
		);
	}

});

module.exports = WallMain;