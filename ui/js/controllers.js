/** initialize  app module **/
var grouphealth = angular.module('grouphealth', ['ui.bootstrap','ngMaterial']);

/**
 * API ping allows us to test the server connection
 * this is called on init to ensure proper server configuration
 **/
grouphealth.controller('ping', ['$scope','API', function($scope,API) {
    $scope.ping = function() {
        API.ping()
        .then(function (data) {
            if (data) {
                // api connection established, log the acknolegment
                console.log(data);
            } else {
                // unable to connect to api, likely server missconfiguration issue
                alert("API Connection Failed");
            }
        });
    }
}]);

/** Handle all appointment CRUD opperations **/
grouphealth.controller('appointment', ['$scope','$mdDialog','$filter','API', function($scope,$mdDialog,$filter,API) {
    // reason options
    $scope.reasons = [
        {name:"CheckUp",value:"CheckUp"},
        {name:"Sick",value:"Sick"},
        {name:"Other",value:"Other"},
    ];

    // local function to refresh list from API, this is called to initialize but also
    // on create/delete (which could and probably should just update locally but this
    // is easier and ensures data is up to date
    function updateList() {
        // get all existing appointments
        API.appointment().get(null)
        .then(function (data) {
            if (data) {
                // update our scoped list
                $scope.list = data;
            } else {
                alert("Failed to GET appointments");
            }
        });
    }

    // initialize list
    updateList();

    // show new appointment dialog
    $scope.create = function(ev) {
        $mdDialog.show({
            controller: DialogController,
            templateUrl: 'view/create.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
        })
    };

    // used by $scope.create
    $scope.showPrerenderedDialog = function(ev) {
        $mdDialog.show({
            contentElement: '#myDialog',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        });
    };

    // used by $scope.create
    function DialogController($scope, $mdDialog, API) {
        $scope.hide = function() {
            $mdDialog.hide();
        };

        // close the dialog, since it has local scope data is cleared/reset for any future open
        $scope.cancel = function() {
            $mdDialog.cancel();
        };

        // parse form data and POST new appointment to the API
        $scope.save = function(form) {
            // form contains alot of meta data and raw date/timestamps
            // re-format just the parts we need
            var postdata = {
                name: form.name,
                reason: form.reason.name,
                date: form.appointment_date,
                date: $filter('date')(form.appointment_date, "yyyy-MM-dd"),
                start: $filter('date')(form.appointment_start, "HH:mm"),
                end: $filter('date')(form.appointment_end, "HH:mm"),
            }

            API.appointment().add(postdata)
            .then(function (data) {
                if (data) {
                    $mdDialog.hide(form);

                    // on successful add, update list to show the item has been added
                    updateList();
                } else {
                    alert("Failed to save appointment");
                }
            });
        };
    }

    // remove an appointment from the list
    $scope.remove = function(id) {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
            .textContent('This cannot be undone. Are you sure?')
            .ok('Remove')
            .cancel('Cancel');

        $mdDialog.show(confirm).then(function() {
            // call API destroy method
            API.appointment().remove(id)
            .then(function (data) {
                if (data) {
                    // alert('Removed '+id);
                    // on successful delete, update list to show the item has been removed
                    updateList();
                } else {
                    alert("Failed to remove appointment "+id);
                }
            });
        }, function() {
            // we don't actually have to do anything on cancel (the dialog closes itself
            // but not having this callback causes a warning in debuggers
        });
    };
}]);

/** input validation bits **/
/*
var app = angular.module('form-example1', []);

var INTEGER_REGEXP = /^-?\d+$/;
app.directive('integer', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.integer = function(modelValue, viewValue) {
        if (ctrl.$isEmpty(modelValue)) {
          // consider empty models to be valid
          return true;
        }

        if (INTEGER_REGEXP.test(viewValue)) {
          // it is valid
          return true;
        }

        // it is invalid
        return false;
      };
    }
  };
});

app.directive('username', function($q, $timeout) {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      var usernames = ['Jim', 'John', 'Jill', 'Jackie'];

      ctrl.$asyncValidators.username = function(modelValue, viewValue) {

        if (ctrl.$isEmpty(modelValue)) {
          // consider empty model valid
          return $q.resolve();
        }

        var def = $q.defer();

        $timeout(function() {
          // Mock a delayed response
          if (usernames.indexOf(modelValue) === -1) {
            // The username is available
            def.resolve();
          } else {
            def.reject();
          }

        }, 2000);

        return def.promise;
      };
    }
  };
});
*/
