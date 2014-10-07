/**
 * @jsx React.DOM
 */

var React = require('react');
var Router = require('react-router');
var Link = Router.Link;

var Friends = React.createClass({

    backgroundImage: function() {
    	return 'https://place.manatee.lc/' + this.props.person.backgroundId + '/1140/250.jpg';
    },

    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

	render: function() {

		console.log(this.props.friends);
		
		var urlirizer = this.urlirizer;
		var friendNodes = this.props.friends.map(function (friend) {
			return (
				<li>
					
					<Link to="wall" params={{ username: friend.username }}>
						<img class="friend-image" src={urlirizer(friend.primaryImageId, 'midi')} />
						{ friend.firstName } { friend.lastName }
					</Link>
				</li>	
			);
		});

		return (
			<div className="friends">
			    
			    <h3>Friends</h3>

			    <ul>
			    	{friendNodes}
			    </ul>
			</div>
		);
	}

});

module.exports = Friends;