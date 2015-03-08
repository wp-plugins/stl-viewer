var loader;
var controls;
var file;

var camera, scene, renderer, mesh_object, mesh_floor, dimensions_x, dimensions_y, dimensions_z, loaded;
var geometry_object;
var effect;

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

var fog_color = '0xd9dee5';
var fog_near = 1;
var fog_far = 10000;

var ambient_light_color = '0x202020';
var ambient_light_intensity = 1;



// IMPORTANT

function $( id ) {
	return document.getElementById( id );
}

	
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
    scene.fog = new THREE.Fog(fog_color, fog_near, fog_far);
				
	// Lights
	scene.add( new THREE.AmbientLight( ambient_light_color, ambient_light_intensity ) );

	directionalLight = new THREE.DirectionalLight(0xffffff, 0.7); 
    directionalLight.position.normalize();
    scene.add(directionalLight);
    
    pointLight = new THREE.PointLight(0xffffff, 0.7);
    scene.add(pointLight);

	// Object
	//var material_object = new THREE.MeshLambertMaterial({color:0xffffff, shading: THREE.FlatShading});
    var material_object = new THREE.MeshPhongMaterial( { ambient: 0x555555, color: 0xffffff, specular: 0x111111, shininess: 200 } );

	//if(inputfiletype == 'STL') loader = new THREE.STLLoader();
	//if(inputfiletype == 'OBJ') loader = new THREE.OBJLoader();
    loader = new THREE.STLLoader();
	loader.load( file, function ( geometry ) {
        geometry_object = geometry;
		mesh_object = new THREE.Mesh( geometry_object, material_object );
		mesh_object.center = geometry_object.center;
					
		geometry.computeBoundingBox();
		dimensions_z = geometry.boundingBox.max.z - geometry.boundingBox.min.z;
		dimensions_y = geometry.boundingBox.max.y - geometry.boundingBox.min.y;
		dimensions_x = geometry.boundingBox.max.x - geometry.boundingBox.min.x;
		mesh_object.castShadow = mesh_object.receiveShadow = true;

        camera.position.set( 0, 0, geometry.boundingSphere.radius*2.2 );
        camera.lookAt(mesh_object.center);

		mesh_object.rotation = object_rotation_offset;
		scene.add( mesh_object );

	} );

	// Floor
	var texture_floor = THREE.ImageUtils.loadTexture( floor );
    texture_floor.wrapS = texture_floor.wrapT = THREE.RepeatWrapping;
    texture_floor.repeat.set( 10, 10 );

	var material_floor = new THREE.MeshBasicMaterial( { map: texture_floor } );
	var geometry_floor = new THREE.PlaneBufferGeometry( 100, 100 );

	mesh_floor = new THREE.Mesh( geometry_floor, material_floor );
	mesh_floor.rotation.x = - Math.PI / 2;
	mesh_floor.scale.set(10, 10, 1);
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

