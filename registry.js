/* Service model */
function Service(data) {
  this.name = ko.observable(data.NAME);
  this.description = ko.observable(data.DESCRIPTION);
  if (Array.isArray(data.METHOD)) {
    this.method = ko.observableArray(data.METHOD);
  }
  else {
    this.method = ko.observableArray([data.METHOD]);
  }
  this.parameters = ko.observable(data.PARAMETERS);
  this.datatypes = ko.observable(data.DATA_TYPE[0]);
  this.url = ko.observable(data._URL);
}

/* Master view model */
function ServiceListViewModel() {
  var self = this;
  //request params, request param types and the selected service
  //are accessible to the popup window model view via getters
  self.requestParams = {};
  self.requestParamTypes = {};        
  self.services = ko.observableArray([]);
  self.selectedService = ko.observable();
  //Retrieve the list of available API calls from the registry service
 // $.getJSON("/MEC/v1/services", function(allData) {
  $.getJSON("/v1/services", function(allData) {
      var mappedServices = $.map(allData, function(item) { return new Service(item) });
      self.services(mappedServices);
  });

  // Called when the user clicks to open the modal dialog 
  //and creates an object that represents the list of request params for the specific service
  //as well as an object which stores the data type for each parameter.
  //(the second is used to fill-in the form placeholders) 
  self.beginExecute = function(selected) {
    self.selectedService(selected);
    $.each(ko.toJS(self.selectedService).parameters, function(index, value) {
      self.requestParams[value] = ko.observable("");
      self.requestParamTypes[value] = ko.observable(ko.toJS(self.selectedService).datatypes[value]);
    });
    $('#execute').modal('show');
  };
  
  self.getSelectedService = function() {
    return self.selectedService;
  };
  self.getRequestParams = function() {    
    return self.requestParams;
  };
  self.getRequestParamTypes = function() {
    return self.requestParamTypes;
  };
}

/* View model for the popup dialog box */
var PopupViewModel = function(vm) {
  var self = this;
  self.selectedService = vm.getSelectedService();
  self.requestParams = vm.getRequestParams();
  self.requestParamTypes = vm.getRequestParamTypes();
  self.selectedMethod = ko.observable("POST");

  /* This is the click handler */        
  self.executeFunction = function() {
    url = self.selectedService().url();
    method = self.selectedMethod();
    params = vm.getRequestParams();

    /* if a parameter value should be integer, check it and convert it */
    $.each(params, function(key, value) {
      if (ko.toJS(self.requestParamTypes[key]).toLowerCase().startsWith("int")) {
        self.requestParams[key] = parseInt(value);
      }
    });

    paramNum = Object.keys(ko.toJS(params)).length;

    if (method == "GET") {
      data = paramNum?ko.toJS(params):undefined;
    }
    else {
      data = JSON.stringify(paramNum?ko.toJS(params):undefined); 
    }

    $.ajax({
      url: url,
      method: ko.toJS(self.selectedMethod),
      data: data,
      processData: true,
      headers: {
          'Content-Type': 'application/json;'
      },
      success: function(result) {
        document.getElementById("resultSuccess").innerHTML = "<pre>" + JSON.stringify(result, null, '  ') + "</pre>";
        document.getElementById("resultSuccess").style="display: block;"
        document.getElementById("resultFailure").innerHTML = JSON.stringify("");
        document.getElementById("resultFailure").style="display: none;"
      },
      error: function(xhr,status,error) {
        document.getElementById("resultFailure").innerHTML = status;
        document.getElementById("resultFailure").style="display: block;"
        document.getElementById("resultSuccess").innerHTML = "";
        document.getElementById("resultSuccess").style="display: none;"
      }
    });
  }
}

//Master view model. Maintains the service list and the currently selected service
var serviceListViewModel = new ServiceListViewModel();
//Popup window view model. We pass the handler to the master view model
//so that the two can communicate. This is necessary to construct
//a form with dynamic fields, depending on the parameters of the selected function call
var popupViewModel = new PopupViewModel(serviceListViewModel);

//Apply bindings for the "master" view and the modal dlg box
ko.applyBindings(serviceListViewModel, $('#main')[0]);
ko.applyBindings(popupViewModel, $('#execute')[0]);

//Clear and hide the status fields when the dialog box is closed
$('#execute').on(
  'show.bs.modal', 
  function (e) {
    document.getElementById("resultSuccess").innerHTML = "";
    document.getElementById("resultFailure").innerHTML = "";
    document.getElementById("resultSuccess").style = "display: none;";
    document.getElementById("resultFailure").style = "display: none;";
});

