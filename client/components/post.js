/**
 * @jsx React.DOM
 */

var React = require('react');

var Comment = require('./comment.js');

var Post = React.createClass({

    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

    author: function(comment) {
    	return comment.poster_first_name + ' ' + comment.poster_last_name;
    },

	render: function() {

		var author = this.author;
	    var commentNodes = this.props.post.comments.map(function(comment) { 
	    	return (<Comment author={author(comment)} image={comment.poster_primary_image_id}>{comment.content}</Comment>);
	    });
	    
		return (
			<li className="post">
				
				<img src={this.urlirizer(this.props.post.poster_primary_image_id, 'midi')}/>
                
                <span dangerouslySetInnerHTML={{ __html: this.props.post.content }}></span>

				<ul className="comments">
					{commentNodes}
				</ul>
			</li>
		);
	}

});

module.exports = Post;