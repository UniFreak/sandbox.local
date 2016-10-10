- well architected applications should expose their low level APIs in an asynchronous fashion as well, especially when they do any sort of I/O or computational heavy processing. like instead of using api like:
`var data=getDate()`
the better api would be
```
getDate(function(data) {
    // process data
})
```

- we can often expose more ergonomic APIs by accepting a single object with multiple properties as a parameter instead of forcing our API consumers to remember the order of many individual parameters