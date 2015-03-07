var loader;
var controls;
var file;

var camera, scene, renderer, mesh_object, mesh_floor, dimensions_x, dimensions_y, dimensions_z, loaded;
var geometry_object, effect;

var rot_offset_x = 0;
var rot_offset_y = 0;
var rot_offset_z = 0;
var object_rotation_offset = new THREE.Euler(rot_offset_x, rot_offset_z, rot_offset_y, 'XZY');


var floor_scale_x = 100;
var floor_scale_y = 100;
var floor_scale_z = 1;
var floor_scale = THREE.Vector3(floor_scale_x, floor_scale_y, floor_scale_z);

var floor_repeat_x = 1;
var floor_repeat_y = 1;
var floor_repeat = THREE.Vector2( floor_repeat_x, floor_repeat_y);

var enable_fog = true;
var fog_color = '0xd9dee5';
var fog_near = 1;
var fog_far = 10000;

var ambient_light_color = '0x202020';
var ambient_light_intensity = 1;



// IMPORTANT

function clone(obj) {
    if(obj == null || typeof(obj) != 'object')
        return obj;

    var temp = obj.constructor(); // changed

    for(var key in obj) {
        if(obj.hasOwnProperty(key)) {
            temp[key] = clone(obj[key]);
        }
    }
    return temp;
}

function $( id ) {
	return document.getElementById( id );
}

function setObjectRotation(x, y, z) {
	mesh_object.rotation.set( x * Math.PI/2, y * Math.PI/2, z * Math.PI/2 );
} // End of setObjectRotation()

	
function cameraPosition() {				// This sets the camera position after loading the geometry.
  	if (geometry_object && !loaded) {
        pointLight.position.set( 0, dimensions_y/2, -2*dimensions_x );
        directionalLight.position.set( 0, dimensions_y * 3/4, dimensions_z * 3 );

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

function STLViewer() {
}
	
function init( inputfiletype ) {
	$( 'progress' ).style.display = 'block';

	renderer = new THREE.WebGLRenderer( { antialias: true } );

	// Camera

	camera = new THREE.PerspectiveCamera( 45, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 25000 );

	// Controls
    controls = new THREE.OrbitControls( camera, container );
	controls.maxPolarAngle = Math.PI/2;
    controls.addEventListener( 'change', render );

    // Init the scene
	scene = new THREE.Scene(); // Scene
    // Fog
    if(enable_fog) {
        scene.fog = new THREE.Fog(fog_color, fog_near, fog_far);
    }
				
	// Lights
	scene.add( new THREE.AmbientLight( ambient_light_color, ambient_light_intensity ) );

	directionalLight = new THREE.DirectionalLight(0xffffff, 0.7); 
    directionalLight.position.normalize();
    scene.add(directionalLight);
    
    pointLight = new THREE.PointLight(0xffffff, 0.7);
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
		mesh_object.castShadow = mesh_object.receiveShadow = true;

		camera.lookAt(mesh_object.center);
        camera.position.set( 0, 0, geometry_object.boundingSphere.radius*2.2 );
        camera.lookAt(mesh_object.center);

		mesh_object.rotation = object_rotation_offset;
		scene.add( mesh_object );

	} ); // End of loader.addEventListener()

	loader.load( file );

	// Floor
	var texture_floor = THREE.ImageUtils.loadTexture( floor );
	var material_floor = new THREE.MeshBasicMaterial( { map: texture_floor } );
	texture_floor.wrapS = texture_floor.wrapT = THREE.RepeatWrapping;
	texture_floor.repeat = floor_repeat;

	var geometry_floor = new THREE.PlaneGeometry( 100, 100 );

	mesh_floor = new THREE.Mesh( geometry_floor, material_floor );
	mesh_floor.rotation.x = - Math.PI / 2;
	mesh_floor.scale.set(1,1,1);
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
	cameraPosition(); // Only runs the first time after mesh is loaded
	mesh_floor.position.y = -dimensions_y/2; //Adjust the height of the floor.
	requestAnimationFrame( animate );
	render();
} // End of animate()

function render() {
	camera.lookAt( scene.position );
	renderer.clear();
	renderer.render( scene, camera );
} // End of render()

