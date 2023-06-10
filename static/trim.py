import bpy

objTrim = bpy.data.objects["Trim"];
objText = bpy.data.objects["Text"];
objDifference = bpy.data.objects["Difference"];
objIntersect = bpy.data.objects["Intersect"];


objText.data.body = "###TEXT###"

objTextMesh = objText.copy()
objTextMesh.data = objText.data.copy()
bpy.context.collection.objects.link(objTextMesh)

bpy.ops.object.select_all(action='DESELECT')
objTextMesh.select_set(True)
bpy.context.view_layer.objects.active = objTextMesh
bpy.ops.object.convert(target="MESH")

boolDiff = objTextMesh.modifiers.new(type="BOOLEAN", name="bool 1")
boolDiff.object = objDifference
boolDiff.operation = 'DIFFERENCE'

boolIntersect = objTextMesh.modifiers.new(type="BOOLEAN", name="bool 2")
boolIntersect.object = objIntersect
boolIntersect.operation = 'INTERSECT'

objTrim.select_set(True)


bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, use_selection=True)