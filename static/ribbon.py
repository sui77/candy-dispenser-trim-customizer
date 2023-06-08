import bpy

objText = bpy.data.objects["Text"]

bpy.ops.object.select_all(action='DESELECT')

# change text
objText.data.body = "###TEXT###"

objText.select_set(True)
bpy.context.view_layer.objects.active = objText
#bpy.ops.object.convert(target="MESH")


# export to stl
bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10)
#bpy.ops.export_mesh.stl(filepa th=str(('x.stl')),   global_scale=10, use_selection=True)