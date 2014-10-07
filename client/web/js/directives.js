'use strict';

/* Directives */

angular.module('losofacebook.directives', []).
  directive('appVersion', ['version', function(version) {
    return function(scope, elm, attrs) {
        elm.text(version);
    };
  }]);


angular.module('losofacebook.directives', []).directive('onEnter', function() {
    return function(scope, element, attrs) {
        element.bind("keydown keypress", function(event) {
            if(event.which === 13) {
                scope.$apply(function(){
                    scope.$eval(attrs.onEnter);
                });

                event.preventDefault();
            }
        });
    };
});

angular.module('losofacebook.directives', [])
    .directive('lbFriends', function factory() {

        var directiveDefinitionObject = {

            restrict: 'E',
            templateUrl: '/views/directives/friends.html',
            replace: true,

            scope: {
                'friends': '=friends'
            },

            link: function postLink(scope, element, attrs) {



            }
        };

        return directiveDefinitionObject;
    })
    .directive('lbWall', function factory(Post, Comment, currentUser, $window, $document, $timeout) {

        var directiveDefinitionObject = {

            restrict: 'E',
            templateUrl: '/views/directives/posts.html',
            replace: true,

            scope: {
                'person': '=person'
            },

            link: function (scope, element, attrs) {

                scope.pager = { 'page': 1, 'limit': 3, hasMore: true }


                scope.fetchMore = function() {

                    if (!scope.pager.hasMore) {
                        return;
                    }

                    scope.pager.page = scope.pager.page + 1;

                    Post.query(
                        { 'person': scope.person.id, 'page': scope.pager.page, 'limit': scope.pager.limit },
                        function (posts) {

                            if (posts.length == 0) {
                                scope.pager.hasMore = false;
                            }

                            angular.forEach(posts, function(post) {
                                scope.posts.push(post);
                            })

                        }
                    )
                };

                scope.doPost = function(person, post) {

                    var post = new Post({
                        'personId': person.id,
                        'content': post,
                        'poster': currentUser,
                        'comments': []
                    });
                    post.$save();

                    scope.posts.unshift(post);
                    this.post = '';

                };

                scope.postComment = function(post, comment) {

                    var comment = new Comment({
                        'postId': post.id,
                        'content': comment,
                        'poster': currentUser
                    });

                    post.comments.push(comment);
                    comment.$save();
                    this.comment = '';
                }

                scope.$watch('person', function(person) {
                    if (person.id) {
                        scope.posts = Post.query({ 'person': person.id, 'page': scope.pager.page, 'limit': scope.pager.limit });
                    }
                }, true);

                var $win = angular.element($window);

                var readyToScroll = true;

                ($win).bind('scroll', function() {

                    var scroll = $win.scrollTop() + $win.height();

                    if (scroll >= $document.height() * 0.90) {
                        if (readyToScroll) {

                            // Really elegant solution... haha!
                            readyToScroll = false;
                            $timeout(function() { 
                                readyToScroll = true;
                            }, 2000, false);

                            scope.fetchMore();
                        }
                    }
                });



            }
        };

        return directiveDefinitionObject;
    })
;




