var loader, controls, file;
	
var camera, scene, renderer, mesh_object, mesh_floor, dimensions_x, dimensions_y, dimensions_z, loaded;
var geometry_object, effect;

function $( id ) {
	return document.getElementById( id );
}

function setObjectRotation(x, y, z) {
	rotation_x = x * Math.PI/2;
	rotation_y = y * Math.PI/2;
	rotation_z = z * Math.PI/2;
	mesh_object.rotation.set( rotation_x, rotation_y, rotation_z );
} // End of setObjectRotation()

	
function cameraPosition() {				// This sets the camera position after loading the geometry.
  	if (geometry_object && !loaded) {
		// ToDo: Adjust these according to camera FOV.
		camera.position.x = dimensions_x/2;
		camera.position.z = dimensions_y*2;
		camera.position.y = dimensions_y/2;

     	point_light.position.x = 0;
     	point_light.position.y = dimensions_y/2;
     	point_light.position.z = -2*dimensions_x;

      	directional_light.position.x = 0;
     	directional_light.position.y = dimensions_y * 3/4;
     	directional_light.position.z = dimensions_z * 3;

		loaded = true; 				//Only run once.

		$( 'progress' ).style.display = 'none';

	} // End of if-command checking for the geometry
} // End of cameraPosition()

function noWebGL() { 	// Runs if no WebGL is found
	$( 'progress' ).style.display = 'none';
	$( 'canvas' ).style.display = 'none';
	$( 'webGLError' ).style.display = 'block';
	$( 'quality_notes' ).style.display = 'none';
} // End of noWebGL()
	
function init( inputfiletype ) {
	$( 'progress' ).style.display = 'block';

	renderer = new THREE.WebGLRenderer( { antialias: true } );

	// Camera
	camera = new THREE.PerspectiveCamera( 35, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 25000 );
	camera.position.y = 100; 					// Default position.
	camera.position.z = 1000;					// The camera position is set with cameraPosition() as soon as the geometry is loaded

	// Controls
    controls = new THREE.OrbitControls( camera, container );
	controls.minPolarAngle = 0;					// Do not rotate under the floor.
	controls.maxPolarAngle = Math.PI/2;
    controls.addEventListener( 'change', render );

	scene = new THREE.Scene(); // Scene
	scene.fog = new THREE.Fog( 0xd9dee5, 1, 10000 ); // Fog
				
	// Lights
	scene.add( new THREE.AmbientLight( 0x202020, 1 ) );

	directional_light = new THREE.DirectionalLight(0xffffff, 0.7);
    directionalLight.position.normalize();
    scene.add(directionalLight);
    
    point_light = new THREE.PointLight(0xffffff, 0.7);
    scene.add(pointLight);

	// Object
	var material_object = new THREE.MeshLambertMaterial({color:0xffffff, shading: THREE.FlatShading});

	if(inputfiletype == 'STL') loader = new THREE.STLLoader();
	if(inputfiletype == 'OBJ') loader = new THREE.OBJLoader();
	loader.addEventListener( 'load', function ( event ) {

		geometry_object = event.content;
		mesh_object = new THREE.Mesh( geometry_object, material_object );
		mesh_object.center = THREE.GeometryUtils.center(geometry_object);
					
		geometry_object.computeBoundingBox();
		dimensions_z = geometry_object.boundingBox.max.z - geometry_object.boundingBox.min.z;
		dimensions_y = geometry_object.boundingBox.max.y - geometry_object.boundingBox.min.y;
		dimensions_x = geometry_object.boundingBox.max.x - geometry_object.boundingBox.min.x;
		mesh_object.castShadow = true;
		mesh_object.receiveShadow = true;                                        
		camera.lookAt(mesh_object.center);
		mesh_object.rotation.set( 0,0,Math.PI );
		scene.add( mesh_object );

	} ); // End of loader.addEventListener()

	loader.load( file );

	// Floor
	var texture_floor = THREE.ImageUtils.loadTexture( floor );
	var material_floor = new THREE.MeshBasicMaterial( { map: texture_floor } );
	texture_floor.wrapS = texture_floor.wrapT = THREE.RepeatWrapping;
	texture_floor.repeat.set( 10, 10);

	var geometry_floor = new THREE.PlaneGeometry( 100, 100 );

	mesh_floor = new THREE.Mesh( geometry_floor, material_floor );
	mesh_floor.rotation.x = - Math.PI / 2;
	mesh_floor.scale.set( 100, 100, 100 );
	mesh_floor.receiveShadow = true;
	scene.add( mesh_floor );

	// RENDERER
	renderer.setSize( SCREEN_WIDTH, SCREEN_HEIGHT );
	renderer.setClearColor( scene.fog.color, 1 );
	renderer.autoClear = false;

	renderer.setSize(container.offsetWidth, container.offsetHeight);
	renderer.domElement.style.position = 'relative';
	container.appendChild( renderer.domElement );

} // End of init()

function animate() {
	cameraPosition();
	mesh_floor.position.y = -dimensions_y/2; //Adjust the height of the floor.
	requestAnimationFrame( animate );
	render();
} // End of animate()

function render() {
	camera.lookAt( scene.position );
	renderer.clear();
	renderer.render( scene, camera );
} // End of render()

