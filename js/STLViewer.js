var loader;
var controls;
var file;

var camera;
var camera_fov;

var mesh_object;
var mesh_floor;

var scene;
var renderer;
var renderer_antialias = true;
var material_object;

var dimensions_x;
var dimensions_y;
var dimensions_z;
var loaded;
var geometry_object;
var material_floor;
var geometry_floor;
var texture_floor;

var rot_offset_x = 0;
var rot_offset_y = 0;
var rot_offset_z = 0;
var object_rotation_offset = new THREE.Euler(rot_offset_x, rot_offset_z, rot_offset_y, 'XZY');

var floor_scale_x = 100;
var floor_scale_y = 100;
var floor_scale_z = 1;
var floor_scale = new THREE.Vector3( floor_scale_x, floor_scale_y, floor_scale_z );
var floor_repeat_x = 10;
var floor_repeat_y = 10;
var floor_repeat = new THREE.Vector2( floor_repeat_x, floor_repeat_y);
var floor;

var fog_color = 0xd9dee5;
var fog_near = 1;
var fog_far = 10000;
var fog;

var ambient_light_color = 0x202020;
var ambient_light;

var point_light;
var point_light_color = 0xffffff;
var point_light_intensity = 0.7;

var directional_light;
var directional_light_color = 0xffffff;
var directional_light_intensity = 0.7;

// IMPORTANT

function $( id ) {
	return document.getElementById( id );
}

function noWebGL() { 	// Runs if no WebGL is found
	$( 'progress' ).style.display = 'none';
	$( 'canvas' ).style.display = 'none';
	$( 'webGLError' ).style.display = 'block';
	$( 'quality_notes' ).style.display = 'none';
} // End of noWebGL()
	
function init( inputfiletype ) {
    $( 'progress' ).style.display = 'block';
    $( 'canvas' ).style.display = 'block';
    $( 'webGLError' ).style.display = 'none';

	camera      = new THREE.PerspectiveCamera( camera_fov, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 25000 );
    controls    = new THREE.OrbitControls( camera, container );
    scene       = new THREE.Scene();
    renderer    = new THREE.WebGLRenderer( { antialias: renderer_antialias } );
    fog         = new THREE.Fog(fog_color, fog_near, fog_far);
    ambient_light       = new THREE.AmbientLight( ambient_light_color );
    directional_light   = new THREE.DirectionalLight(directional_light_color, directional_light_intensity);
    point_light         = new THREE.PointLight(point_light_color, point_light_intensity);
    material_object     = new THREE.MeshLambertMaterial({color:0xffffff, shading: THREE.FlatShading});
    //material_object = new THREE.MeshPhongMaterial( { ambient: 0x555555, color: 0xffffff, specular: 0x111111, shininess: 200 } );

    texture_floor   = THREE.ImageUtils.loadTexture( floor );
    material_floor  = new THREE.MeshBasicMaterial( { map: texture_floor } );
    geometry_floor  = new THREE.PlaneGeometry( 100, 100 );
    mesh_floor      = new THREE.Mesh( geometry_floor, material_floor );

    loader = new THREE.STLLoader();
    //if(inputfiletype == 'STL') loader = new THREE.STLLoader();
    //if(inputfiletype == 'OBJ') loader = new THREE.OBJLoader();

    controls.maxPolarAngle = Math.PI/2;
    controls.addEventListener( 'change', render );

    scene.fog = fog;
				
	scene.add( ambient_light );
    directional_light.position.normalize();
    scene.add(directional_light);
    scene.add(point_light);

	loader.load( file, function ( geometry ) {
        geometry_object = geometry;
		mesh_object = new THREE.Mesh( geometry_object, material_object );
        mesh_object.castShadow = mesh_object.receiveShadow = true;

        geometry.computeBoundingBox();
        geometry.computeBoundingSphere();
		dimensions_z = geometry.boundingBox.max.z - geometry.boundingBox.min.z;
		dimensions_y = geometry.boundingBox.max.y - geometry.boundingBox.min.y;
		dimensions_x = geometry.boundingBox.max.x - geometry.boundingBox.min.x;

        point_light.position.set( 0, dimensions_y / 2, - 2 * dimensions_x );
        directional_light.position.set( 0, dimensions_y * 3 / 4, dimensions_z * 3 );

        camera.position.set( 0, 0, geometry.boundingSphere.radius * 2.2 );
        camera.lookAt(0,0,0);
		mesh_object.rotation.copy(object_rotation_offset);

        mesh_floor.position.y = - dimensions_y / 2;
        mesh_object.position.x = - dimensions_x / 2;

		scene.add( mesh_object );

	} );

	// Floor
    texture_floor.wrapS = texture_floor.wrapT = THREE.RepeatWrapping;
    texture_floor.repeat.copy( floor_repeat );

	mesh_floor.rotation.x = - Math.PI / 2;
	mesh_floor.scale.copy( floor_scale );
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
    if (geometry_object && !loaded) {
        loaded = true;
        $( 'progress' ).style.display = 'none';
    }
    requestAnimationFrame( animate );
	render();
} // End of animate()

function render() {
	camera.lookAt( scene.position );
	renderer.clear();
	renderer.render( scene, camera );
} // End of render()

function STLViewer() {
    init('STL');
    animate();
}

