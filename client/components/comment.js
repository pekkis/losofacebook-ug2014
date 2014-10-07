/**
 * @jsx React.DOM
 */

var React = require('react');

var Comment = React.createClass({

	propTypes: {
		author: React.PropTypes.string.isRequired,
		image: React.PropTypes.string.isRequired
	},

    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

	render: function() {

		return (
			<li className="comment">
				<img src={this.urlirizer(this.props.image, 'midi')}/>
				<div dangerouslySetInnerHTML={{ __html: this.props.children }}></div>
			</li>
		);
	}

});

module.exports = Comment;