/**
 * @jsx React.DOM
 */

var React = require('react');

var Comment = require('./comment.js');

var Post = React.createClass({

    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

	render: function() {

	    var commentNodes = this.props.post.comments.map(function(comment) { 
	    	return (<Comment comment={comment}></Comment>);
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