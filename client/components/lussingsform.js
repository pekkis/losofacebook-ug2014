/**
 * @jsx React.DOM
 */

var React = require('react');

var LussingsForm = React.createClass({

	handleSubmit: function(e) {
    	e.preventDefault();
    	var lussing = this.refs.lussing.getDOMNode().value.trim();
    	if (!lussing) {
      		return;
    	}
    	this.props.onLussingSubmit(lussing);
    	this.refs.lussing.getDOMNode().value = '';
    	return;
  	},

	render: function() {
		return (
			<form role="form" onSubmit={this.handleSubmit}>
				<textarea ref="lussing" placeholder="lussutus be here..." className="form-control"></textarea>
			    <button className="btn btn-primary" type="submit">Luss something</button>
			</form>
		);
	}

});

module.exports = LussingsForm;