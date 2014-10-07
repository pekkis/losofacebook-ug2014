/**
 * @jsx React.DOM
 */

var React = require('react');

var Comment = React.createClass({

    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

	render: function() {

		return (
			<li className="comment">
				<img src={this.urlirizer(this.props.comment.poster_primary_image_id, 'midi')}/>
				<span dangerouslySetInnerHTML={{ __html: this.props.comment.content }}></span>
			</li>
		);
	}

});

module.exports = Comment;