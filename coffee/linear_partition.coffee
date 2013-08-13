# Linear partition
# Partitions a sequence of non-negative integers into k ranges
# Based on Óscar López implementation in Python (http://stackoverflow.com/a/7942946)
# Also see http://www8.cs.umu.se/kurser/TDBAfl/VT06/algorithms/BOOK/BOOK2/NODE45.HTM
# Dependencies: UnderscoreJS (http://www.underscorejs.org)
# Example: linear_partition([9,2,6,3,8,5,8,1,7,3,4], 3) => [[9,2,6,3],[8,5,8],[1,7,3,4]]

# identity = (x) -> x

# max = (obj, iterator, context) ->
#   if not iterator
#     return Math.max.apply Math, obj
  
#   result = 
#     computed: -Infinity
#     value: -Infinity

#   for value, index in obj
#     computed = iterator.call context, value, index, obj

#     if computed > result.computed
#       result =
#         value: value
#         computed: computed
  
#   result.value

# min = (obj, iterator, context) ->
#   if not iterator
#     return Math.min.apply Math, obj
  
#   result = 
#     computed: -Infinity
#     value: -Infinity

#   for index, value in obj
#     computed = iterator.call context, value, index, obj

#     if computed < result.computed
#       result =
#         value: value
#         computed: computed
  
#   result.value

each = (obj, iterator, context) ->
  obj.forEach iterator, context

max = `function(obj, iterator, context) {
  if (!iterator && obj[0] === +obj[0] && obj.length < 65535) {
    return Math.max.apply(Math, obj);
  }
  var result = {computed : -Infinity, value: -Infinity};
  each(obj, function(value, index, list) {
    var computed = iterator ? iterator.call(context, value, index, list) : value;
    computed > result.computed && (result = {value : value, computed : computed});
  });
  return result.value;
}`

min = `function(obj, iterator, context) {
  if (!iterator && obj[0] === +obj[0] && obj.length < 65535) {
    return Math.min.apply(Math, obj);
  }
  var result = {computed : Infinity, value: Infinity};
  each(obj, function(value, index, list) {
    var computed = iterator ? iterator.call(context, value, index, list) : value;
    computed < result.computed && (result = {value : value, computed : computed});
  });
  return result.value;
}`

window.linear_partition = linear_partition = (seq, k) =>
  n = seq.length
  
  return [] if k <= 0
  return seq.map((x) -> [x]) if k > n
 
  table = (0 for x in [0...k] for y in [0...n])
  solution = (0 for x in [0...k-1] for y in [0...n-1])
  table[i][0] = seq[i] + (if i then table[i-1][0] else 0) for i in [0...n]
  table[0][j] = seq[0] for j in [0...k]
  for i in [1...n]
    for j in [1...k]
      m = min(([max([table[x][j-1], table[i][0]-table[x][0]]), x] for x in [0...i]), (o) -> o[0])
      table[i][j] = m[0]
      solution[i-1][j-1] = m[1]
 
  n = n-1
  k = k-2
  ans = []
  while k >= 0
    ans = [seq[i] for i in [(solution[n-1][k]+1)...n+1]].concat ans
    n = solution[n-1][k]
    k = k-1
 
  [seq[i] for i in [0...n+1]].concat ans


test = linear_partition [9,2,6,3,8,5,8,1,7,3,4], 3
control = [[9,2,6,3],[8,5,8],[1,7,3,4]]

console.assert (JSON.stringify test) == (JSON.stringify control)