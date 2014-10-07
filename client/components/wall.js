/**
 * @jsx React.DOM
 */

var React = require('react');

var WallHeader = require('./wallheader.js');
var WallMain = require('./wallmain.js');

var Wall = React.createClass({

    getInitialState: function() {
    	return {
    		person: {
    			'username': '',
    		}
    	};
    },

    componentDidMount: function() {

	    $.get('http://api.losofacebook.tunk.io/api/person/' + this.props.params.username, function(person) {

	        this.setState({
	        	'person': person
	        });

	    }.bind(this));
    },

	componentWillReceiveProps: function(props) {
	    
	    $.get('http://api.losofacebook.tunk.io/api/person/' + props.params.username, function(person) {
	        this.setState({
	        	'person': person
	        });

	    }.bind(this));
	  },

	render: function() {
				
		if (!this.state.person.username) {
			return (<div>Laddar</div>);
		}

		return (
			<div className="padder">
				<WallHeader person={this.state.person}></WallHeader>
				<WallMain person={this.state.person}></WallMain>
			</div>
		);
	}

});

module.exports = Wall;