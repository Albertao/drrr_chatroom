/**
 * Created by albert on 16-5-3.
 */

//register some global variables
var ws_server = "ws://localhost:9777";

//factory the whole app
var drrr_app = angular.module('drrr', [
    'drrr_controllers',
    'ngRoute'
]);

//routes configurations
drrr_app.config(['routeProvider', function ($routeProvider) {
    $routeProvider.when(

    ).when(

    ).otherwise(

    );
}]);