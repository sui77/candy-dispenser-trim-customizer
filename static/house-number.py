import bpy

objText = bpy.data.objects["Text.001"]

bpy.ops.object.select_all(action='DESELECT')

# change text
objText.data.body = "###TEXT###"

objText.select_set(True)
bpy.context.view_layer.objects.active = objText
#bpy.ops.object.convert(target="MESH")

#
# export to stl
bpy.ops.wm.stl_export(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, up_axis='Y')
